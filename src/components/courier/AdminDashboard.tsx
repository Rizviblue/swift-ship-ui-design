import { Package, Truck, CheckCircle, XCircle, TrendingUp, Users } from "lucide-react";
import { StatsCard } from "./StatsCard";
import { RecentCouriers } from "./RecentCouriers";
import { Button } from "@/components/ui/button";

// Mock data
const mockStats = [
  {
    title: "Total Couriers",
    value: 1248,
    icon: Package,
    trend: { value: 12, label: "from last month", isPositive: true }
  },
  {
    title: "In Transit",
    value: 186,
    icon: Truck,
    trend: { value: 8, label: "from yesterday", isPositive: true }
  },
  {
    title: "Delivered",
    value: 892,
    icon: CheckCircle,
    trend: { value: 15, label: "this week", isPositive: true }
  },
  {
    title: "Cancelled",
    value: 24,
    icon: XCircle,
    trend: { value: 3, label: "from last week", isPositive: false }
  }
];

const mockRecentCouriers = [
  {
    id: "1",
    trackingNumber: "CMS001234",
    sender: "John Doe",
    receiver: "Alice Smith",
    destination: "New York",
    status: "in-transit" as const,
    createdAt: "2 hours ago"
  },
  {
    id: "2",
    trackingNumber: "CMS001235",
    sender: "Bob Johnson",
    receiver: "Carol Brown",
    destination: "Los Angeles",
    status: "delivered" as const,
    createdAt: "5 hours ago"
  },
  {
    id: "3",
    trackingNumber: "CMS001236",
    sender: "David Wilson",
    receiver: "Eva Davis",
    destination: "Chicago",
    status: "pending" as const,
    createdAt: "1 day ago"
  },
  {
    id: "4",
    trackingNumber: "CMS001237",
    sender: "Frank Miller",
    receiver: "Grace Lee",
    destination: "Houston",
    status: "cancelled" as const,
    createdAt: "2 days ago"
  }
];

export function AdminDashboard() {
  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold text-foreground">Admin Dashboard</h1>
          <p className="text-muted-foreground">Monitor and manage your courier operations</p>
        </div>
        <div className="flex space-x-3">
          <Button variant="outline">
            <TrendingUp className="w-4 h-4 mr-2" />
            Analytics
          </Button>
          <Button>
            <Package className="w-4 h-4 mr-2" />
            Add Courier
          </Button>
        </div>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {mockStats.map((stat, index) => (
          <StatsCard
            key={stat.title}
            {...stat}
            className={`animation-delay-${index * 100}`}
          />
        ))}
      </div>

      {/* Content Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Recent Couriers */}
        <div className="lg:col-span-2">
          <RecentCouriers couriers={mockRecentCouriers} />
        </div>

        {/* Quick Actions & Summary */}
        <div className="space-y-6">
          {/* Quick Actions */}
          <div className="glass-card rounded-xl p-6 animate-slide-up">
            <h3 className="text-lg font-semibold text-foreground mb-4">Quick Actions</h3>
            <div className="space-y-3">
              <Button className="w-full justify-start" variant="outline">
                <Package className="w-4 h-4 mr-2" />
                Add New Courier
              </Button>
              <Button className="w-full justify-start" variant="outline">
                <Users className="w-4 h-4 mr-2" />
                Manage Agents
              </Button>
              <Button className="w-full justify-start" variant="outline">
                <TrendingUp className="w-4 h-4 mr-2" />
                View Reports
              </Button>
            </div>
          </div>

          {/* Activity Summary */}
          <div className="glass-card rounded-xl p-6 animate-slide-up">
            <h3 className="text-lg font-semibold text-foreground mb-4">Today's Activity</h3>
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <span className="text-sm text-muted-foreground">New Couriers</span>
                <span className="text-sm font-medium text-foreground">23</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-sm text-muted-foreground">Deliveries</span>
                <span className="text-sm font-medium text-success">45</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-sm text-muted-foreground">Active Agents</span>
                <span className="text-sm font-medium text-foreground">12</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-sm text-muted-foreground">Revenue</span>
                <span className="text-sm font-medium text-foreground">$2,840</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}