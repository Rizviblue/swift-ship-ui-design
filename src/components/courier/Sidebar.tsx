import { useState } from "react";
import { NavLink, useLocation } from "react-router-dom";
import { 
  LayoutDashboard, 
  Package, 
  Users, 
  UserCheck, 
  Plus, 
  BarChart3, 
  Settings,
  Menu,
  X,
  Truck,
  FileText
} from "lucide-react";
import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";

interface SidebarProps {
  userRole: 'admin' | 'agent' | 'customer';
}

export function Sidebar({ userRole }: SidebarProps) {
  const [collapsed, setCollapsed] = useState(false);
  const location = useLocation();

  const adminMenuItems = [
    { title: "Dashboard", url: "/admin", icon: LayoutDashboard },
    { title: "Add Courier", url: "/admin/add-courier", icon: Plus },
    { title: "Courier List", url: "/admin/couriers", icon: Package },
    { title: "Agent Management", url: "/admin/agents", icon: UserCheck },
    { title: "Customer Management", url: "/admin/customers", icon: Users },
    { title: "Reports", url: "/admin/reports", icon: BarChart3 },
    { title: "Settings", url: "/admin/settings", icon: Settings },
  ];

  const agentMenuItems = [
    { title: "Dashboard", url: "/agent", icon: LayoutDashboard },
    { title: "My Couriers", url: "/agent/couriers", icon: Package },
    { title: "Update Status", url: "/agent/update", icon: Truck },
    { title: "Reports", url: "/agent/reports", icon: FileText },
  ];

  const customerMenuItems = [
    { title: "Track Courier", url: "/customer", icon: Package },
    { title: "My Orders", url: "/customer/orders", icon: FileText },
    { title: "Contact", url: "/customer/contact", icon: Users },
  ];

  const menuItems = userRole === 'admin' ? adminMenuItems : 
                   userRole === 'agent' ? agentMenuItems : customerMenuItems;

  const isActive = (path: string) => location.pathname === path;

  return (
    <div className={cn(
      "h-screen bg-card border-r border-border transition-all duration-300 shadow-soft",
      collapsed ? "w-16" : "w-64"
    )}>
      {/* Header */}
      <div className="p-4 border-b border-border">
        <div className="flex items-center justify-between">
          {!collapsed && (
            <div className="flex items-center space-x-2">
              <div className="w-8 h-8 rounded-lg gradient-primary flex items-center justify-center">
                <Package className="w-5 h-5 text-white" />
              </div>
              <div>
                <h2 className="font-semibold text-foreground">CourierPro</h2>
                <p className="text-xs text-muted-foreground capitalize">{userRole} Panel</p>
              </div>
            </div>
          )}
          <Button
            variant="ghost"
            size="sm"
            onClick={() => setCollapsed(!collapsed)}
            className="p-2"
          >
            {collapsed ? <Menu className="w-4 h-4" /> : <X className="w-4 h-4" />}
          </Button>
        </div>
      </div>

      {/* Navigation */}
      <nav className="p-4 space-y-2">
        {menuItems.map((item) => (
          <NavLink
            key={item.title}
            to={item.url}
            className={({ isActive }) => cn(
              "flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-all duration-200 group",
              isActive 
                ? "bg-primary text-primary-foreground shadow-sm" 
                : "text-muted-foreground hover:bg-muted hover:text-foreground"
            )}
          >
            <item.icon className="w-5 h-5 flex-shrink-0" />
            {!collapsed && (
              <span className="font-medium">{item.title}</span>
            )}
          </NavLink>
        ))}
      </nav>
    </div>
  );
}