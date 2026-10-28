<?php
namespace App\Controllers\User;

use App\Controllers\Controller;
use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DashboardController extends Controller
{

    public function index(Request $request, Response $response, array $args): Response
    {

        return $this->html($request, 'dashboard/index');

    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $userId = $request->getAttribute('user_id');

        $userHelper = new \App\Helpers\User();
        $role       = $userHelper->get_role($userId);

        $stats = [
            'total'     => 0,
            'published' => 0,
            'pending'   => 0,
            'verified'  => 0,
            'recent'    => [],
        ];

        if (! $role) {
            return $this->json($stats);
        }

        // Base conditions
        $where = [
            't1.deleted_at' => null,
        ];

        // Role-based listing access
        $roleColumns = [
            'subscriber' => ['created_by', 'owner_id'],
            'bdm'        => ['created_by', 'owner_id', 'bdm_id'],
            'bde'        => ['created_by', 'owner_id', 'bde_id'],
        ];

        if (isset($roleColumns[$role])) {
            $or = [];

            foreach ($roleColumns[$role] as $column) {
                $or["t1.{$column}"] = $userId;
            }

            $where['OR'] = $or;
        }

        // Count helper
        $countListings = function (array $conditions) {
            return $this->db->count('listings(t1)', '*', $conditions);
        };

        $stats['total'] = $countListings($where);

        // Published: published but not yet verified
        $stats['published'] = $countListings(array_merge($where, [
            't1.published_at[!]' => null,
            't1.verified_at'     => null,
        ]));

        // Pending: neither published nor verified
        $stats['pending'] = $countListings(array_merge($where, [
            't1.published_at' => null,
            't1.verified_at'  => null,
        ]));

        // Verified: published and verified
        $stats['verified'] = $countListings(array_merge($where, [
            't1.published_at[!]' => null,
            't1.verified_at[!]'  => null,
        ]));

        // Recent listings
        $recentWhere = array_merge($where, [
            'ORDER' => ['t1.id' => 'DESC'],
            'LIMIT' => [0, 10],
        ]);

        $recentListings = $this->db->select(
            'listings(t1)',
            '*',
            $recentWhere
        );

        $recent = [];

        foreach ($recentListings as $listing) {
            $listingHelper = new \App\Helpers\Listing($listing);

            $status        = $listingHelper->get_status();
            $category      = $listingHelper->get_category();
            $subcategories = $listingHelper->get_subcategories();
            $city          = $listingHelper->get_city();
            $areas         = $listingHelper->get_areas();
            $recent[]      = [
                'slug'          => $listing['slug'],
                'name'          => $listing['name'],
                'contacted'     => $listing['contacted'],
                'viewed'        => $listing['viewed'],
                'served'        => $listing['served'],
                'ratings'       => $listing['ratings'],
                'status'        => $status,
                'category'      => $category,
                'city'          => $city,
                'subcategories' => $subcategories,
                'areas'         => $areas,
                'updatedAt'     => Carbon::parse($listing['updated_at'])->format('M d, Y'),
            ];
        }

        $stats['recent'] = $recent;

        return $this->json($stats);
    }
}
